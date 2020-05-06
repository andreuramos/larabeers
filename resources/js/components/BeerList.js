import React, { Component } from 'react';
import BeerListItem from "./BeerListItem";

export default class BeerList extends Component
{
    render() {
        const empty_list = this.props.beers.length === 0;
        if (empty_list){
            return (
                <div className="list-group"><span>No results</span></div>
            );
        }
        const beerList = this.props.beers.map(function(beer) {
            return <BeerListItem beer={beer} key={beer.id} />;
        });

        return (
            <div className="list-group">
                { beerList }
            </div>
        );
    }
}
