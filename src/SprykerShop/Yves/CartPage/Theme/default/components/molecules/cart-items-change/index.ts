import register from 'ShopUi/app/registry';
export default register(
    'cart-items-change',
    () =>
        import(
            /* webpackMode: "lazy" */
            /* webpackChunkName: "cart-items-change" */
            './cart-items-change'
        ),
);
