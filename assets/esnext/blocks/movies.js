import { PostSelector } from '../components/PostSelector';

const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { ServerSideRender, TextControl, RangeControl, SelectControl, CheckboxControl } = wp.components;
// const { select } = wp.data;
// const { addQueryArgs } = wp.url;

registerBlockType( 'masvideos/movies', {
    title: 'Movies Block',

    icon: 'megaphone',

    category: 'widgets',

    edit: ( ( props ) => {
        const { attributes, className, setAttributes } = props;
        const { limit, columns, orderby, order, ids, featured, top_rated } = attributes;

        // let selectedPostIds = ids ? ids.split(',').map(Number) : [];

        // const { getEntity, getEntityRecords } = select( 'core' );
        // let query = {
        //     search: "2",
        //     per_page: -1,
        // };
        // const movies = getEntityRecords( 'postType', 'movie', query );
        // const categories = getEntityRecords( 'taxonomy', 'movie_cat', query );
        // console.log( movies );
        // console.log( categories );

        // const movies = wp.apiFetch( {
        //     path: addQueryArgs( '/wp/v2/movie', {
        //         search: "2",
        //         per_page: -1,
        //     } ),
        // } );
        // console.log( movies );
        
        // const getPostTypes = wp.apiFetch( {
        //     path: '/wp/v2/types',
        // } );
        // console.log( getPostTypes );

        const onChangeLimit = newLimit => {
            setAttributes( { limit: newLimit } );
        };

        const onChangeColumns = newColumns => {
            setAttributes( { columns: newColumns } );
        };

        const onChangeOrderby = newOrderby => {
            setAttributes( { orderby: newOrderby } );
        };

        const onChangeOrder = newOrder => {
            setAttributes( { order: newOrder } );
        };

        const onChangeIds = newIds => {
            setAttributes( { ids: newIds.join(',') } );
        };

        const onChangeFeatured = newFeatured => {
            setAttributes( { featured: newFeatured } );
        };

        const onChangeTopRated = newTopRated => {
            setAttributes( { top_rated: newTopRated } );
        };

        return [
            <InspectorControls>
                <RangeControl
                    label="Limit"
                    value={ limit }
                    onChange={ onChangeLimit }
                    min={ 1 }
                    max={ 50 }
                />
                <RangeControl
                    label="Columns"
                    value={ columns }
                    onChange={ onChangeColumns }
                    min={ 1 }
                    max={ 10 }
                />
                <SelectControl
                    label="Orderby"
                    value={ orderby }
                    options={ [
                        { label: 'Title', value: 'title' },
                        { label: 'Date', value: 'date' },
                        { label: 'ID', value: 'id' },
                        { label: 'Random', value: 'rand' },
                    ] }
                    onChange={ onChangeOrderby }
                />
                <SelectControl
                    label="Order"
                    value={ order }
                    options={ [
                        { label: 'ASC', value: 'ASC' },
                        { label: 'DESC', value: 'DESC' },
                    ] }
                    onChange={ onChangeOrder }
                />
                <PostSelector
                    postType = 'movie'
                    selectedPostIds={ ids ? ids.split(',').map(Number) : [] }
                    updateSelectedPostIds={ onChangeIds }
                />
                <CheckboxControl
                    label="Featured"
                    help="Check to select featured movies."
                    checked={ featured }
                    onChange={ onChangeFeatured }
                />
                <CheckboxControl
                    label="Top Rated"
                    help="Check to select top rated movies."
                    checked={ top_rated }
                    onChange={ onChangeTopRated }
                />
            </InspectorControls>,
            <ServerSideRender
                block="masvideos/movies"
                attributes={ attributes }
            />
        ];
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );