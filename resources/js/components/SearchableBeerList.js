import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import SearchInput from "./SearchInput";
import BeerList from "./BeerList";
import {randomBeers} from "../api";

export default class SearchableBeerList extends Component {
    constructor() {
        super();
        this.state = {
            query: null,
            message: 'Showing random beers',
            beers: null,
            loading: true,
        };
        randomBeers().then(data => {
            this.setState({
                loading: false,
                message: "showing " + data.length + ' random beers',
                beers: data,
            });
        });
    }


    searchBeers(query) {

    }

    render() {
        return (
            <div className="card mt-3">
                <div className="card-header">
                    <SearchInput placeholder="Find Beers" />
                </div>
                <div className="card-body px-0 px-md-2">
                    <span> { this.state.message }</span>
                    <div className="container">
                    {this.state.loading ?
                        <i className="fa fa-spinner fa-spin"/> :
                        <BeerList beers={this.state.beers} />
                    }
                    </div>
                </div>
            </div>
        );
    }
}

if (document.getElementById('searchableBeerList')) {
    ReactDOM.render(<SearchableBeerList />, document.getElementById('searchableBeerList'));
}
