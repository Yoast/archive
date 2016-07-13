# yoastseo-node
Node app to run YoastSEO on multiple texts

to run the analyzer:

```
npm install
node analyse.js
```

The app assumes a local mysql exists with a database called `yoastseo`, if it doesn't, on your mac run:

```
brew install mysql
```

Start mysql, create a database called `yoastseo` and you can use `example.sql` to prefill the database.
