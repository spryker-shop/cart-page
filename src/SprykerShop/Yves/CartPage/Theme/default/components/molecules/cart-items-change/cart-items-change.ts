import Component from 'ShopUi/models/component';
import AjaxProvider, { EVENT_FETCHED, EVENT_FETCHING } from 'ShopUi/components/molecules/ajax-provider/ajax-provider';

export interface CartLine {
    groupKey: string;
    quantity: number;
    [key: string]: unknown;
}

// `quantity` of each line is the changed amount.
export interface CartItemsChangeDetail {
    increased: CartLine[];
    decreased: CartLine[];
}

/**
 * Compares the cart lines before an async cart action with the lines the re-rendered cart shows,
 * and announces the added and removed quantities once.
 */
export default class CartItemsChange extends Component {
    protected provider: AjaxProvider;
    protected cartLinesBefore: Map<string, CartLine> | null = null;

    protected init(): void {
        this.provider = <AjaxProvider>document.getElementsByClassName(this.providerClassName)[0];

        if (!this.provider || !this.eventName) {
            return;
        }

        this.mapEvents();
    }

    protected mapEvents(): void {
        this.provider.addEventListener(EVENT_FETCHING, () => this.onFetching());
        this.provider.addEventListener(EVENT_FETCHED, () => this.onFetched());
    }

    protected onFetching(): void {
        // A cart action sent while another is pending replaces it, and the cart renders once: both are measured from the lines before the first.
        if (this.cartLinesBefore) {
            return;
        }

        this.cartLinesBefore = this.readCartLines();
    }

    protected onFetched(): void {
        // The ajax renderer renders the response in its own listener of the same event, so the lines are read once every listener ran.
        Promise.resolve().then(() => this.announceCartItemsChange());
    }

    protected announceCartItemsChange(): void {
        const cartLinesBefore = this.cartLinesBefore;
        this.cartLinesBefore = null;

        if (!cartLinesBefore) {
            return;
        }

        const cartItemsChange = this.calculateCartItemsChange(cartLinesBefore, this.readCartLines());

        if (!cartItemsChange.increased.length && !cartItemsChange.decreased.length) {
            return;
        }

        this.dispatchEvent(
            new CustomEvent<CartItemsChangeDetail>(this.eventName, { bubbles: true, detail: cartItemsChange }),
        );
    }

    protected calculateCartItemsChange(
        cartLinesBefore: Map<string, CartLine>,
        cartLinesAfter: Map<string, CartLine>,
    ): CartItemsChangeDetail {
        const cartItemsChange: CartItemsChangeDetail = { increased: [], decreased: [] };

        for (const [groupKey, cartLineAfter] of cartLinesAfter) {
            const quantityDifference = cartLineAfter.quantity - (cartLinesBefore.get(groupKey)?.quantity ?? 0);

            if (quantityDifference > 0) {
                cartItemsChange.increased.push({ ...cartLineAfter, quantity: quantityDifference });
            }

            if (quantityDifference < 0) {
                cartItemsChange.decreased.push({ ...cartLineAfter, quantity: -quantityDifference });
            }
        }

        // A removed line is only in the cart before the action, so its details come from there.
        for (const [groupKey, cartLineBefore] of cartLinesBefore) {
            if (!cartLinesAfter.has(groupKey) && cartLineBefore.quantity > 0) {
                cartItemsChange.decreased.push(cartLineBefore);
            }
        }

        return cartItemsChange;
    }

    protected readCartLines(): Map<string, CartLine> {
        const cartLines = new Map<string, CartLine>();
        const target = document.getElementsByClassName(this.targetClassName)[0];

        if (!target) {
            return cartLines;
        }

        for (const cartLineElement of Array.from(target.querySelectorAll(this.cartLineSelector))) {
            const cartLine = this.parseCartLine(cartLineElement.getAttribute('tracking-details'));

            if (cartLine && !cartLines.has(cartLine.groupKey)) {
                cartLines.set(cartLine.groupKey, cartLine);
            }
        }

        return cartLines;
    }

    protected parseCartLine(trackingDetails: string | null): CartLine | null {
        try {
            const cartLine = JSON.parse(trackingDetails ?? '');
            const isCartLine = typeof cartLine?.groupKey === 'string' && cartLine.groupKey !== '';

            return isCartLine ? { ...cartLine, quantity: Number(cartLine.quantity) || 0 } : null;
        } catch {
            return null;
        }
    }

    protected get eventName(): string | null {
        return this.getAttribute('event-name');
    }

    protected get providerClassName(): string {
        return this.getAttribute('provider-class-name');
    }

    protected get targetClassName(): string {
        return this.getAttribute('target-class-name');
    }

    protected get cartLineSelector(): string {
        return this.getAttribute('cart-line-selector');
    }
}
