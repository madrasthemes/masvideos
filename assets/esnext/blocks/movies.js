const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const { ServerSideRender, TextControl, RangeControl, SelectControl, CheckboxControl } = wp.components;
const { withSelect } = wp.data;

registerBlockType( 'masvideos/movies', {
    title: 'Movies Block',

    icon: 'megaphone',

    category: 'widgets',

    edit: withSelect( ( select ) => {
        return {
            movies: select( 'core' ).getEntityRecords( 'postType', 'movie' )
        };
    } )( ( props ) => {
        const { attributes, className, setAttributes, movies } = props;
        const { limit, columns, orderby, order, featured, top_rated } = attributes;
        // console.log( movies );

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