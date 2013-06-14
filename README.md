#q.uote.me

*PHP script to store and view random quotes.*

Q.uote.me is a simple PHP script to manage your quotes.

Actually, you have your favorite quotes in multiple txt files, one for your mails, another for your web app, etc...

When you find a new quote, you have to update your files... It can be boring !

With this script, you manage only one SQL database. With his API, you read it in your favorite format (actualy, json, rss2, csv).

##Features
**Storage** : Store your favorite quotes in a single place,  

**API** : you can extract quotes with sql like queries in multiples formats,  
_Supported_ : **RSS, JSON, CSV**. in the future : XML, TXT, ATOM and _iCal_ ;),  

[WIP]

###Updates
_2013-06-14 :_
- Added permalinks,
- Added creation date (in the future maybe changed by quote date),
- Added tags.

###Bugfixes
_2013-06-14 :_
- Fixed RSS (dates, links other errors like {item} in source,
- Foreach control for json and csv parser.
