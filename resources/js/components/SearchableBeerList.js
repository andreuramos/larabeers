import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import SearchInput from "./SearchInput";
import BeerList from "./BeerList";
import {randomBeers, searchBeers} from "../api";

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

        this.searcherKeyUp = this.searcherKeyUp.bind(this);
    }

    searcherKeyUp(event) {
        const query = event.target.value;
        if (query.length < 3) {
            return;
        }
        this.setState({ loading: true, message: null });
        searchBeers(query).then( data => {
            this.setState({
                loading: false,
                message: "showing " + data.length + ' results',
                beers: data,
            });
        })
    }

    render() {
        return (
            <div className="card mt-3">
                <div className="card-header">
                    <SearchInput placeholder="Find Beers" handleKeyUp={this.searcherKeyUp} />
                </div>
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

if (document.getElementById('searchableBeerList')) {
    ReactDOM.render(<SearchableBeerList />, document.getElementById('searchableBeerList'));
}
