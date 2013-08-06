#Q.uote.me

*PHP script to store and view random quotes. (v1.5b)*

Q.uote.me is a simple PHP script to manage your quotes.

Actually, you have your favorite quotes in multiple txt files, one for your mails, another for your web app, etc...

When you find a new quote, you have to update your files... It can be boring !

With this script, you manage only one SQL database. With his API, you read it in your favorite format (actualy, json, rss2, csv).

##Features
**Storage** : Store your favorite quotes in a single place,  

**API** : you can extract quotes with sql like queries in multiples formats,  
_Supported_ : **RSS2, JSON, CSV, IMG**. in the future : XML, TXT, ATOM and _iCal_ ;),  

##How to use

###_Api.php :_

To extract datas, use api.php (ex: http://q.uote.me/api.php) and use the following syntax in url :  
**p** > parser : json rss2 csv img _ex : p=json_  
**s** > sort : data,asc desc or random _ex : s=date,desc s=random_  
**l** > limit : like sql syntax _ex : l=10 l=5,25_  
**w** > where : like sql syntax _ex : w=quote_ must be followed by:  
**wo** > where options, the first part of the option can be **minus = <**, **plus = >**, **equal = =** and **like** _ex : w=quote&wo=like,lorem_  
**a** > and : like (and must be preceded by) **where**  
**ao** > and second part (and option) like **and option**

####Availables columns :  
**quote** _(text)_, **author** _(varchar)_, **source** _(varchar)_, **tags** _(varchar)_, **permalink** _(char 6)_, **date** _(datetime)_

####Image extraction options :
**t** > type : png gif jpeg (or all image supported by your phpgd version) _ex : t=png_  
**wi** > width : width of image in pixel _ex : wi=512_  
_If you want extract a png image, options are not required_  

####Full examples :  

- To extract a random quote in json format :  
**http://q.uote.me/api.php?p=json**
- To extract a quote in csv format :  
**http://q.uote.me/api.php?p=csv&w=permalink&wo=equal,xHlefA**
- To extract last 10 quotes in rss2 format :  
**http://q.uote.me/api.php?p=rss2&s=date,desc&l=10**
- To extract the 10 following quotes :  
**http://q.uote.me/api.php?p=rss2&s=date,desc&l=11,21**
- Fun, one extraction with all options :  
**http://q.uote.me/api.php?p=json&w=quote&wo=like,a&a=author&ao=like,douat&s=date,asc&l=5&s=random**
- To extract a png file (actually just random quote) :  
**http://q.uote.me/api.php?p=img**  
- To extract a jpeg file of 500 pixels width :  
**http://q.uote.me/api.php?p=img&t=jpeg&wi=512**  

###_Index of website :_

Just type address of website to view random quote. Click on the arrow to reload a new quote. Add permalink option in the URL to view a specific quote.  
Permalink format example : http://q.uote.me?xHlefA (thank to [sebsauvage](https://github.com/sebsauvage/Shaarli) for permalink function)

###Updates :

_2013-08-06 (1.5b)_
- Added cache for non radom requests,
- Added ajax update on the index,
- Added api doc on api.php call.

_2013-07-26 (1.4b)_
- Added image parser.

_2013-07-04 (1.3b):_
- Added installation script,
- Fixed many bugs.

_2013-06-30 (1.2b):_
- Added languages support.

_2013-06-28 (1.1b):_
- Added daily check update for script.

_2013-06-23 (1.0b):_
- Added login module,
- Added administration,
- Optimized quoteme lib.

_2013-06-14 :_
- Added permalinks,
- Added creation date (in the future maybe changed by quote date),
- Added tags.

###Bugfixes :
_2013-06-14 :_
- Fixed RSS (dates, links other errors like {item} in source,
- Foreach control for json and csv parser.

##Licence :

Q.uote.me is distributed under the zlib/libpng License :  

Copyright (c) 2013 Daniel Douat, [Aélys Informatique](http://aelys-info.fr)  

This software is provided 'as-is', without any express or implied warranty. In no event will the authors be held liable for any damages arising from the use of this software.  

Permission is granted to anyone to use this software for any purpose, including commercial applications, and to alter it and redistribute it freely, subject to the following restrictions :  

1. The origin of this software must not be misrepresented; you must not claim that you wrote the original software. If you use this software in a product, an acknowledgment in the product documentation would be appreciated but is not required.  

2. Altered source versions must be plainly marked as such, and must not be misrepresented as being the original software.  

3. This notice may not be removed or altered from any source distribution.
