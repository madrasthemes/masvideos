import { PostSelector } from '../components/PostSelector';
import { TermSelector } from '../components/TermSelector';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { ServerSideRender, TextControl, RangeControl, SelectControl, CheckboxControl } = wp.components;

registerBlockType( 'masvideos/movies', {
    title: __('Movies Block', 'masvideos'),

    icon: 'format-video',

    category: 'masvideos-blocks',

    edit: ( ( props ) => {
        const { attributes, className, setAttributes } = props;
        const { limit, columns, orderby, order, ids, category, featured, top_rated } = attributes;

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

        const onChangeCategory = newCategory => {
            setAttributes( { category: newCategory.join(',') } );
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
                    label={__('Limit', 'masvideos')}
                    value={ limit }
                    onChange={ onChangeLimit }
                    min={ 1 }
                    max={ 50 }
                />
                <RangeControl
                    label={__('Columns', 'masvideos')}
                    value={ columns }
                    onChange={ onChangeColumns }
                    min={ 1 }
                    max={ 10 }
                />
                <SelectControl
                    label={__('Orderby', 'masvideos')}
                    value={ orderby }
                    options={ [
                        { label: __('Title', 'masvideos'), value: 'title' },
                        { label: __('Date', 'masvideos'), value: 'date' },
                        { label: __('ID', 'masvideos'), value: 'id' },
                        { label: __('Random', 'masvideos'), value: 'rand' },
                    ] }
                    onChange={ onChangeOrderby }
                />
                <SelectControl
                    label={__('Order', 'masvideos')}
                    value={ order }
                    options={ [
                        { label: __('ASC', 'masvideos'), value: 'ASC' },
                        { label: __('DESC', 'masvideos'), value: 'DESC' },
                    ] }
                    onChange={ onChangeOrder }
                />
                <PostSelector
                    postType = 'movie'
                    selectedPostIds={ ids ? ids.split(',').map(Number) : [] }
                    updateSelectedPostIds={ onChangeIds }
                />
                <TermSelector
                    postType = 'movie'
                    taxonomy = 'movie_cat'
                    selectedTermIds={ category ? category.split(',').map(Number) : [] }
                    updateSelectedTermIds={ onChangeCategory }
                />
                <CheckboxControl
                    label={__('Featured', 'masvideos')}
                    help={__('Check to select featured movies.', 'masvideos')}
                    checked={ featured }
                    onChange={ onChangeFeatured }
                />
                <CheckboxControl
                    label={__('Top Rated', 'masvideos')}
                    help={__('Check to select top rated movies.', 'masvideos')}
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