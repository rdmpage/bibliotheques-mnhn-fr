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

For each journal this table lists the ISSN for that journal, it’s Wikidata Qid, BHL TitleID, link to an example of MNHN digitisation, coverage visualisations based on BioStor, years journal published, and links to spreadsheets with article metadata.

Title | ISSN | Wikidata | BHL | mnhn.fr | Coverage | Years | Spreadsheets
-- | -- | -- | -- | -- | -- | -- | --
A |
Adansonia nouvelle série | 0001-804X  | [Q58814054](https://alec-demo.herokuapp.com/?id=Q58814054) | [169110](https://www.biodiversitylibrary.org/bibliography/169110#/summary) | [ADANS_S000](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=ADANS_S000_1961_T001_N001) | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/169110.png) [new](https://rdmpage.github.io/bhl-article-coverage/169110.html) [old](http://direct.biostor.org/issn/0001-804X) | 1961-1980 | [BioStor](http://direct.biostor.org/issn/1280-8571.tsv)
Adansonia | 1280-8571 | [Q5735523](https://alec-demo.herokuapp.com/?id=Q5735523) | [149832](https://www.biodiversitylibrary.org/title/149832) | | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/149832.png) [new](https://rdmpage.github.io/bhl-article-coverage/149832.html) [old](http://direct.biostor.org/issn/1280-8571)| 1997 - | [BioStor](http://direct.biostor.org/issn/1280-8571.tsv)
B |
Bulletin du Musée d'Histoire Naturelle | 0027-4070 | Q5735526 | [68686](https://www.biodiversitylibrary.org/bibliography/68686) | [BUMHN_S001](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=BUMHN_S001_1895_T001_N001) | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/68686.png) [new](https://rdmpage.github.io/bhl-article-coverage/68686.html)  | 1895-1906| [BioStor](http://direct.biostor.org/issn/0376-4443.tsv) [BioStor](http://direct.biostor.org/issn/1148-8425.tsv)
Bulletin du Muséum national d'histoire naturelle | 0027-4070, 1148-8425 | Q37408733 | [5943](https://www.biodiversitylibrary.org/bibliography/5943) | [BMNHN_S001](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=BMNHN_S001_1907_T013_N002) | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/5943.png) [new](https://rdmpage.github.io/bhl-article-coverage/5943.html)  | 1907-1970| [BioStor](http://direct.biostor.org/issn/0376-4443.tsv) [BioStor](http://direct.biostor.org/issn/1148-8425.tsv)
Bulletin du Muséum National d'Histoire Naturelle Sér. 3, Botanique | 0376-4443 | Q51404910 | [12908](https://www.biodiversitylibrary.org/bibliography/12908) |  | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/12908.png) [new](https://rdmpage.github.io/bhl-article-coverage/12908.html) [old](http://direct.biostor.org/issn/0376-4443)| 1972-1978 | [BioStor](http://direct.biostor.org/issn/0376-4443.tsv)
Bulletin du Muséum national d'histoire naturelle. Section B, Botanique,biologie et écologie végétales, phytochimie | 0181-0634 | Q37513749 | [14109](https://www.biodiversitylibrary.org/bibliography/14109) |  | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/14109.png) [new](https://rdmpage.github.io/bhl-article-coverage/14109.html) [old](http://direct.biostor.org/issn/0181-0634)| 1979-1980| [BioStor](http://direct.biostor.org/issn/0181-0634.tsv)
Bulletin du Muséum National d'Histoire Naturelle Section B,Adansonia, botanique, phytochimie | 0240-8937 | Q51412421 | [13855](https://www.biodiversitylibrary.org/bibliography/13855) | [BMBAD_S004](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=BMBAD_S004_1987_T009_N002) | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/13855.png) [new](https://rdmpage.github.io/bhl-article-coverage/13855.html) [old](http://direct.biostor.org/issn/0240-8937)| 1981-1996| [BioStor](http://direct.biostor.org/issn/0240-8937.tsv)
Bulletin du Muséum national d'Histoire naturelle 3ème série - Zoologie | 0300-9386 | Q36778289 | [149559](https://www.biodiversitylibrary.org/bibliography/149559) | [BMZOO_S003](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=BMZOO_S003_1971_T001_N001)| ![Coverage](https://rdmpage.github.io/bhl-article-coverage/149559.png) [new](https://rdmpage.github.io/bhl-article-coverage/149559.html) [old](http://direct.biostor.org/issn/0300-9386)| 1971-1978 | [BioStor](http://direct.biostor.org/issn/0300-9386.tsv)
Bulletin du Muséum national d'Histoire naturelle, 4ème série - Section A - Zoologie | 0181-0626  | Q21385899 | [158834](https://www.biodiversitylibrary.org/bibliography/158834) | [BMAZO_S004](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=BMAZO_S004_1992_T014_N002)| ![Coverage](https://rdmpage.github.io/bhl-article-coverage/158834.png) [new](https://rdmpage.github.io/bhl-article-coverage/158834.html) [old](http://direct.biostor.org/issn/0181-0626)| 1979-1992| [BioStor](http://direct.biostor.org/issn/0181-0626.tsv)
Bulletin du Museum national d'histoire naturelle Sciences de la terre | 0376-446X | Q51418653 | [145272](https://www.biodiversitylibrary.org/bibliography/145272) | | | 1971-1978| .
Bulletin du Muséum national d'Histoire naturelle, 4ème série - Section C |  |  |  | | | | .
Bulletin du Muséum National d'Histoire Naturelle Supplément |  |  |  | | | | .
C |
G |
M |
N |
Notulae Systematicae | 0374-9223 | [Q6045778](https://alec-demo.herokuapp.com/?id=Q6045778) | [314](https://www.biodiversitylibrary.org/bibliography/314) | NOTUL | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/314.png) [new](https://rdmpage.github.io/bhl-article-coverage/314.html) [old](http://direct.biostor.org/issn/0374-9223) | 1909-1960 | [local](https://github.com/rdmpage/bibliotheques-mnhn-fr/raw/main/tsv/Notulae%20Systematicae.tsv)
R |
Revue de mycologie  | 0484-8578 | [Q39125612](https://alec-demo.herokuapp.com/?id=Q39125612) | [169397](https://www.biodiversitylibrary.org/bibliography/169397) | [MNHN_REMYC](https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=MNHN_REMYC_1979_T043_N004) | ![Coverage](https://rdmpage.github.io/bhl-article-coverage/169397.png) [new](https://rdmpage.github.io/bhl-article-coverage/169397.html) [old](http://direct.biostor.org/issn/0484-8578) | |[BioStor](http://direct.biostor.org/issn/0484-8578.tsv)

Z |
Zoosystema |  |  |  | | | | .




