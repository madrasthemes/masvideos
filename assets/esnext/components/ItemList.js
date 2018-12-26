import { Item } from './Item';

/**
 * ItemList Component
 * @param object props - Component props.
 * @returns {*}
 * @constructor
 */
export const ItemList = props => {
    const { filtered = false, loading = false, items = [], action = () => {}, icon = null } = props;

    if (loading) {
        return <p className="loading-items">Loading ...</p>;
    }

    if (filtered && items.length < 1) {
        return (
            <div className="item-list">
                <p>Your query yielded no results, please try again.</p>
            </div>
        );
    }

    if ( ! items || items.length < 1 ) {
        return <p className="no-items">Not found.</p>
    }

    return (
        <div class="item-list">
            {items.map((item) => <Item key={item.id} {...item} clickHandler={action} icon={icon} />)}
        </div>
    );
};