import React, { Component } from 'react';
import BeerListItem from "./BeerListItem";

export default class BeerList extends Component
{
    render() {
        const beers = this.props.beers.map(function(beer) {
            return <BeerListItem beer={beer} key={beer.id} />;
        });

        return (
            <div className="beer-list">
                { beers }
            </div>
        );
    }
}
