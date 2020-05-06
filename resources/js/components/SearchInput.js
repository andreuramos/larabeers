import React, { Component } from 'react';

export default class SearchInput extends Component
{
    constructor(props) {
        super(props);
        this.state = {
            query: null
        }
    }

    render() {
        return (
            <React.Fragment>
                <i className="fa fa-search mr-2"></i>
                <input
                    type="text"
                    placeholder={this.props.placeholder}
                    onKeyUp={this.props.handleKeyUp}
                    />
            </React.Fragment>
        )
    }
}
