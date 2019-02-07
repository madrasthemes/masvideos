const { apiFetch } = wp;

/**
 * Makes a get request to the PostTypes endpoint.
 *
 * @returns {Promise<any>}
 */
export const getPostTypes = () => {
    return apiFetch( { path: '/wp/v2/types' } );
};

/**
 * Makes a get request to the desired post type and builds the query string based on an object.
 *
 * @param {string|boolean} restBase - rest base for the query.
 * @param {object} args
 * @returns {Promise<any>}
 */
export const getPosts = ({ restBase = false, ...args }) => {
    const queryString = Object.keys(args).map(arg => `${arg}=${args[arg]}`).join('&');

    let path = `/wp/v2/${restBase}?${queryString}&_embed`;
    return apiFetch( { path: path } );
};

/**
 * Makes a get request to the PostType Taxonomies endpoint.
 *
 * @returns {Promise<any>}
 */
export const getTaxonomies = ({ ...args }) => {
    const queryString = Object.keys(args).map(arg => `${arg}=${args[arg]}`).join('&');

    let path = `/wp/v2/taxonomies?${queryString}&_embed`;
    return apiFetch( { path: path } );
};

/**
 * Makes a get request to the desired post type and builds the query string based on an object.
 *
 * @param {string|boolean} restBase - rest base for the query.
 * @param {object} args
 * @returns {Promise<any>}
 */
export const getTerms = ({ restBase = false, ...args }) => {
    const queryString = Object.keys(args).map(arg => `${arg}=${args[arg]}`).join('&');

    let path = `/wp/v2/${restBase}?${queryString}&_embed`;
    return apiFetch( { path: path } );
};