import React, { Component } from 'react';

export default class BeerListItem extends Component
{
    constructor(props) {
        super(props);
        this.state = {
            picture_loading: true
        };
        this.setLoaded = this.setLoaded.bind(this);
    }

    setLoaded() {
        this.setState({
            picture_loading: false
        });
    }

    render() {
        const picture_placeholder = "img/label-template.jpg";
        return (
            <div className="px-0 px-md-2">
                <div className="col-12 beer-list__beer">
                    <div className="beer-list__beer__image">
                        <img
                            src={picture_placeholder}
                            style={!this.state.picture_loading ? {visibility: 'hidden', width: 0} : null}
                        />
                        <img
                            src={this.props.beer.thumbnail}
                            style={this.state.picture_loading ? {visibility: 'hidden', width: 0} : null}
                            onLoad={this.setLoaded}
                        />
                    </div>
                    <div className="beer-list__beer__data">
                        <div className="beer-list__beer__name">
                            <a href={'/beer/'  + this.props.beer.id}>{ this.props.beer.name }</a>
                        </div>
                        <span className="beer-list__beer__data__flag">
                            <img className="country-flag" src={this.props.beer.flag} title="country_name_here"/>
                        </span>
                        <span className="beer-list__beer__data__brewer">{ this.props.beer.brewer }</span>
                        <div
                            className="badge badge-secondary beer-list__beer__data__year-badge"
                            style={this.props.beer.year ? {} : {visibility: 'hidden'}}
                        > { this.props.beer.year }</div>
                    </div>
                </div>
            </div>
        )
    }
}
