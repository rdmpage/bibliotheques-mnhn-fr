# Bibliothèques Muséum National d’Histoire Naturelle

Metadata for content hosted by the [Muséum National d’Histoire Naturelle library](https://bibliotheques.mnhn.fr).

This repository tracks progress on extracting article-level metadata and mapping that to the [Biodiversity Heritage Library](https://www.biodiversitylibrary.org) (BHL) and [Wikidata](https://www.wikidata.org/wiki/Wikidata:Main_Page).

![image](https://github.com/rdmpage/bibliotheques-mnhn-fr/raw/main/reading/frise.png)

## Background

The MNHN library website has scans of journals, together with some metadata (such as article titles). One goal of this project is to extract that metadata, enhance it with pagination, etc., then map it to BHL (typically using [BioStor](https://biostor.org)).

Another goal is to enhance author information for these publications in Wikidata.

## Authors

There are tools for disambiguating authors in Wikidata and converting authors from “strings” to “things”, such as [Author Disambiguator](https://author-disambiguator.toolforge.org) and [Ozymandias](https://ozymandias-demo.herokuapp.com/wikidata-match.php?q=A+Aubréville).

Here is an example query to find authors for articles in a journal, listing the Wikidata id for the author (if known) and the IDRef (if it is in Wikidata). [Try it here](https://w.wiki/ox9).

## Journals

See [Kanban board](https://github.com/rdmpage/bibliotheques-mnhn-fr/projects/1) for progress.


## Google Docs

- [BMBAD](https://docs.google.com/spreadsheets/d/1uJ1xdUELtySimIbkkDnnLoetIf4tH4_fTOxa8-0Z-os/edit?usp=sharing)
- [BMBOT](https://docs.google.com/spreadsheets/d/1X5L9stlGJv5L8OvgoWVfvXw-T8OtOkDUVANmv4QqqMw/edit?usp=sharing)
- [BMAZO](https://docs.google.com/spreadsheets/d/1EveQSugk9PMGZT80gIKGd6HNfEx0mrfzyrFaBPn2zSU/edit?usp=sharing)
- [Faune de Madagascar](https://docs.google.com/spreadsheets/d/19lbe-x5zNgErcetTESrCt-CPQN89zvPCprFTtNf-uQY/edit?usp=sharing)
- [NOTUL](https://docs.google.com/spreadsheets/d/1u8ElhqqrMyBvj89K9Y811ZhRCB0NU1HztV1Khx1-JhU/edit?usp=sharing)

## SQL

```
SELECT * FROM `publications_mnhn` WHERE issn="0240–8937" ORDER BY CAST(volume AS SIGNED), CAST(issue AS SIGNED), CAST(scan_page AS SIGNED);
```


SUDOC and other book-like works

```
SELECT guid, title, journal, issn, series, volume, issue, spage, epage, authors, year, wikidata, PageID, biostor, oclc, internetarchive FROM `publications` WHERE issn="0428-0709" ORDER BY year, CAST(volume AS SIGNED), CAST(spage AS SIGNED);
```

## How to

### Fetch files

From the MNHN site find volumes of a journal, e.g. https://bibliotheques.mnhn.fr/medias/search.aspx?instance=EXPLOITATION&SC=IFD_BIBNUM&QUERY=Cryptogamie%2C+Mycologie#/Search/%7B%22query%22%3A%7B%22ForceSearch%22%3Afalse%2C%22Page%22%3A0%2C%22QueryString%22%3A%22Cryptogamie%2C+Mycologie%22%2C%22ResultSize%22%3A50%2C%22ScenarioCode%22%3A%22IFD_BIBNUM%22%2C%22SearchLabel%22%3A%22%22%2C%22SortField%22%3Anull%2C%22SortOrder%22%3A0%7D%7D

Extract the links, then fetch the HTML `fetchhtml.php` and the PDFs `fetchpdfs.php`.

### Parse

`parse-simple.php` to extract basic metadata

`parse-text.php` to try and get page details from PDFs.

### Export



