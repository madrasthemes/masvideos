const { __ } = wp.i18n;
const { Component, Children } = wp.element;
const { Button, Icon } = wp.components;

/**
 * Repeater Component
 */
export class Repeater extends Component {
    /**
     * Constructor for Repeater Component.
     * Sets up state, and creates bindings for functions.
     * @param object props - current component properties.
     */
    constructor(props) {
        super(...arguments);
        this.props = props;

        this.state = {
            values: [],
        };

        this.renderAddButton = this.renderAddButton.bind(this);
        this.renderRemoveButton = this.renderRemoveButton.bind(this);
        this.handleAdd = this.handleAdd.bind(this);
        this.handleRemove = this.handleRemove.bind(this);
        this.renderChildrenElements = this.renderChildrenElements.bind(this);
    }

    /**
     * Fetches children from parent
     */
    componentDidMount() {
        const { values } = this.props;
        if( values ) {
            this.setState({
                values: values,
            });
        }
    }

    renderAddButton() {
        return(
            <Button isDefault onClick={this.handleAdd}>
                <Icon icon="plus" />
            </Button>
        );
    }

    renderRemoveButton() {
        return(
            <Button isDefault onClick={this.handleRemove}>
                <Icon icon="minus" />
            </Button>
        );
    }

    handleAdd() {
        const { defaultValues, updateValues } = this.props;
        const { values } = this.state;
        const current_values = values ? [ ...values, { ...defaultValues } ] : [ { ...defaultValues } ];
        this.setState({
            values: current_values,
        });
        updateValues( current_values );
    }

    handleRemove( index ) {
        const { updateValues } = this.props;
        const { values } = this.state;
        const current_values = values.filter( ( value, i ) => i != index );
        this.setState({
            values: current_values,
        });
        updateValues( current_values );
    }

    renderChildrenElements( values, children ) {
        if( ! values ) {
            return [];
        }

        const remove_button = this.renderRemoveButton();

        return values.map( ( value, index ) => {
            const updated_children = Children.map(children, ( child ) => {
                let child_props = { ...child.props };
                if( values[index][child.props.name] ) {
                    child_props[child.props.valuekey] = values[index][child.props.name];
                }
                child_props[child.props.trigger_method_name] = (value) => child.props[child.props.trigger_method_name](value, index);
                return React.cloneElement( child, { ...child_props } );
            } );

            const updated_remove_button = React.cloneElement( remove_button, { key: 'repeater-remove-'+index, onClick: () => remove_button.props['onClick'](index) } );

            return React.createElement('div', { key: 'repeater-child-'+index }, [updated_children, updated_remove_button]);
        } );
    }

    /**
     * Renders the Repeater component.
     */
    render() {
        const { title, children } = this.props;
        const { values } = this.state;
        
        const childrenWithProps = this.renderChildrenElements( values, children );

        return (
            <div>
                {title}
                {childrenWithProps}
                {this.renderAddButton()}
            </div>
        );
    }
}