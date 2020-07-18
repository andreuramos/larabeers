import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import BeerList from "./BeerList";
import { findBeer } from '../api';

export default class FixedBeerList extends Component {
    constructor(props) {
        super(props);
        console.log("beer ids", this.props.beer_ids);
        this.state = {
            loading: true,
            beers: null,
        };

        findBeer(this.props.beer_ids).then( data => {
            this.setState({
                loading: false,
                message: "showing " + data.length + ' beers',
                beers: data,
            });
        });
    }

    render() {
        return (
            <div className="card mt-3">
                <div className="card-body px-0 px-md-2">
                    <span className="ml-4"> { this.state.message }</span>
                    <div className="container mt-2">
                        {this.state.loading ?
                            <i className="loading fa fa-spinner fa-spin"/> :
                            <BeerList beers={this.state.beers} />
                        }
                    </div>
                </div>
            </div>
        );
    }
}

if (document.getElementById('fixedBeerList')) {
    var beer_ids = document.getElementById('fixedBeerList').getAttribute('beer_ids');
    ReactDOM.render(<FixedBeerList beer_ids={beer_ids} />, document.getElementById('fixedBeerList'));
}
