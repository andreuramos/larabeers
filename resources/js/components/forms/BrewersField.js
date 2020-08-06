import React, { Component } from 'react';
import ReactDOM from "react-dom";

export default class BrewersField extends Component
{
    constructor(props) {
        super(props);
    }

    render() {
        console.log(this.props.brewer_ids);
        return (
            <div>I'm a brewers field</div>
        )
    }
}

if (document.getElementById('brewers-field')) {
    var brewer_ids = document.getElementById('brewers-field').getAttribute('brewer_ids');
    ReactDOM.render(<BrewersField brewer_ids={brewer_ids} />, document.getElementById('brewers-field'));
}
