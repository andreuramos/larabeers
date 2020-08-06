## Beer Sticker collection Manager

Personal project to manage a **beer sticker collection** made with Laravel 5 and React.

### Features

- Summary of the collection stats
- Search for beers in the collection (now implemented with React)
- Location distribution of the beers
- Upload beers from a csv
- Insert a single beer
- Beer & Brewer page
- Show images hosted in external storage

### Future Features

- Improve location & location stats (countries, provinces, cities , ...)
- Implement other "CDN" integration other than Google Drive

It can be accessed [here](http://larabeers.herokuapp.com)

## Testing

There are 2 test suites in this app: backend and frontend.

### Backend

For now only the domain and a few infrastructure classes have tests. To run them:

```
$ vendor/bin/phpunit
``` 

This will execute all the suites configured in the `phpunit.xml` file, setting up the database as `mysqlite` so make sure you have it installed or dokerized

### Frontend

React components will be tested with:

```
$ npm test
```

This will execute all the `xxx.spec.js` files in the project. For now there are only a few components with Jest test.
