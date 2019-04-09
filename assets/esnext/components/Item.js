
/**
 * Item Component.
 *
 * @param {string} itemTitle - Current item title.
 * @param {function} clickHandler - this is the handling function for the add/remove function
 * @param {Integer} itemId - Current item ID
 * @param icon
 * @returns {*} Item HTML.
 */
export const Item = ({ title: { rendered: itemTitle } = {}, name, clickHandler, id: itemId, icon }) => (
    <article className="item">
        <div className="item-body">
            <h3 className="item-title">{itemTitle}{name}</h3>
        </div>
        <button onClick={() => clickHandler(itemId)}>{icon}</button>
    </article>
);