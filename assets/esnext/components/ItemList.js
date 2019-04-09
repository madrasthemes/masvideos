import { Item } from './Item';

const { __ } = wp.i18n;

/**
 * ItemList Component
 * @param object props - Component props.
 * @returns {*}
 * @constructor
 */
export const ItemList = props => {
    const { filtered = false, loading = false, items = [], action = () => {}, icon = null } = props;

    if (loading) {
        return <p className="loading-items">{__('Loading ...', 'vodi')}</p>;
    }

    if (filtered && items.length < 1) {
        return (
            <div className="item-list">
                <p>{__('Your query yielded no results, please try again.', 'vodi')}</p>
            </div>
        );
    }

    if ( ! items || items.length < 1 ) {
        return <p className="no-items">{__('Not found.', 'vodi')}</p>
    }

    return (
        <div className="item-list">
            {items.map((item) => <Item key={item.id} {...item} clickHandler={action} icon={icon} />)}
        </div>
    );
};