# dokuwiki-abbrlist
List abbreviation plugin for [DokuWiki](https://www.dokuwiki.org/dokuwiki) 

## Installation

Search and install the plugin using the [Extension Manager](https://www.dokuwiki.org/plugin:extension). Refer to [Plugins](https://www.dokuwiki.org/plugins) on how to install plugins manually.

## Examples/Usage

Add the code `{{abbrlist>}}` to any Wiki page to display a list of defined acronyms.

## Syntax

You can specify several options to influence the behaviour of the plugin.

```
{{abbrlist>nointernal,sort}}
```

This omits the abbreviations defined within DokuWiki and only lists user-defined acronyms (i.e. those in acronyms.local.conf). If you specify `sort`, the results are sorted by acronym.
