// Import SCSS entry file so that webpack picks up changes
import './index.scss';

console.log( 'hello world' );
import { useEffect, useRef, useState } from '@wordpress/element';
import { WooNavigationItem } from '@woocommerce/navigation';
import { registerPlugin } from '@wordpress/plugins';
import { Button } from '@wordpress/components';
import { addFilter, addAction } from '@wordpress/hooks';
import {
    H,
    Section,
    Table,
} from '@woocommerce/components';
 
// // Build a functional React component using JSX.
// const MyExtenstionNavItem = () => (
//     <WooNavigationItem item="my-extension">
//         <Button>Hello From JavaScript</Button>
//     </WooNavigationItem>
// );
 
// // Use the registerPlugin function to register your component with the navigation 
// registerPlugin( 'my-extension', { render: MyExtenstionNavItem } );


// addAction( 'admin_menu', 'register_navigation_items' );
const fetchRecentProductData = async ( setRecentProductData = () => void null ) => {
    const reviewsUrl = '/wc/v3/products'
 
    await wp.apiFetch({
        path: reviewsUrl
    }).then( (data) => {
        console.log(data);
            setRecentProductData( data );
        });
}

const MyExamplePage = () => {
    // Using static data for demonstration.
    const [recentProductData, setRecentProductData] = useState([]);
 
    const headers = [
        { label: 'Product ID' },
        { label: 'Rating' },
        { label: 'Date Created' },
    ]
    const rowData = recentProductData.map( ( product ) => {
    return [
            { display: product?.id, value: product?.id },
            { display: product.average_rating, value: product.average_rating },
            { display: product.date_created, value: product.date_created }
        ]
  });
 
    useEffect(() => {
        fetchRecentProductData(setRecentProductData);
    }, []);
 
    return (
        <>
            <H>My Example Extension</H>
            <Section component='article'>
                <p>This is a table of recent reviews</p>
                <Table
                    caption= "Recent Reviews"
                    rows = { rowData }
                    headers = { headers }
                />
            </Section>
        </>
    )
}
 
addFilter( 'woocommerce_admin_pages_list', 'my-namespace', ( pages ) => {
    pages.push( {
        container: MyExamplePage,
        path: '/example',
        breadcrumbs: [ 'My Extension Page' ],
        navArgs: {
            id: 'my-example-page',
        },
    } );
 
    return pages;
} );


addFilter('woocommerce_publish_product', 'my-block-handler', (productId) => {
    // Perform actions when a new product is published
    console.log('Product published:', productId);
    // You can perform additional actions here, such as sending notifications or updating related data.
    return productId; // Return the product ID
});



// import { registerBlockType } from '@wordpress/blocks';


// console.log("block2");
// const MyExampleBlock = () => {
//     const [count, setCount] = useState(0);
//     return <div>This is my count {count}</div>

// }
// // Register the block
// registerBlockType( 'gutenberg-examples/example-01-basic-esnext', {
//     title: 'Test Demo Block2',
 
//     icon: 'megaphone',
 
//     category: 'common',
//     edit: function () {
//         return <MyExampleBlock />;
//     },
//     save: function () {
//         return <MyExampleBlock />;
//     },
// } );
