#Q.uote.me

*PHP script to store and view random quotes.*

Q.uote.me is a simple PHP script to manage your quotes.

Actually, you have your favorite quotes in multiple txt files, one for your mails, another for your web app, etc...

When you find a new quote, you have to update your files... It can be boring !

With this script, you manage only one SQL database. With his API, you read it in your favorite format (actualy, json, rss2, csv).

##Features
**Storage** : Store your favorite quotes in a single place,  

**API** : you can extract quotes with sql like queries in multiples formats,  
_Supported_ : **RSS2, JSON, CSV**. in the future : XML, TXT, ATOM and _iCal_ ;),  

##How to use

###_Api.php :_

To extract datas, use api.php (ex: http://q.uote.me/api.php) and use the following syntax in url :  
**p** > parser : json rss2 csv _ex : p=json_  
**s** > sort : data,asc desc or random _ex : s=date,desc s=random_  
**l** > limit : like sql syntax _ex : l=10 l=5,25_  
**w** > where : like sql syntax _ex : w=quote_ must be followed by:  
**wo** > where options, the first part of the option can be **minus = <**, **plus = >**, **equal = =** and **like** _ex : w=quote&wo=like,lorem_  
**a** > and : like (and must be preceded by) **where** 
**ao** > and second part (and option) like **and option**

####Availables columns :  
**quote** _(text)_, **author** _(varchar)_, **source** _(varchar)_, **tags** _(varchar)_, **permalink** _(char 6)_, **date** _(datetime)_

####Full examples :  

- To extract a random quote in json format :  
**http://q.uote.me/api.php?p=json**
- To extract a quote in csv format :  
**http://q.uote.me/api.php?p=csv&w=permalink&wo=equal,XxU_7**
- To extract last 10 quotes in rss2 format :  
**http://q.uote.me/api.php?p=rss2&s=date,desc&l=10**
- TO extract the 10 following quotes :  
**http://q.uote.me/api.php?p=rss2&s=date,desc&l=11,21**
- Fun, one extraction with all options :  
**http://q.uote.me/api.php?p=json&w=quote&wo=like,a&a=author&ao=like,douat&s=date,asc&l=5&s=random**

###_Index of website :_

[WIP]

###Updates :
_2013-06-14 :_
- Added permalinks,
- Added creation date (in the future maybe changed by quote date),
- Added tags.

###Bugfixes :
_2013-06-14 :_
- Fixed RSS (dates, links other errors like {item} in source,
- Foreach control for json and csv parser.
