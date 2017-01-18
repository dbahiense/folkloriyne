# folkloriyne

folkloriyne is a searching tool that... NGA FOLKLORI YNË

## TODOs:

- create result list with the following information:
	- [title], [volume], [page], [nr]
	- Category: [category]
	- Story collect in [year]
	- Information about the teller:
	- [name]
	- [text]

- how to transform location in a google maps link: 
	- 42.58594080,20.73099880 = https://www.google.com.br/maps/place//@42.5859408,20.7309988,9z/data=!4m5!3m4!1s0x0:0x0!8m2!3d42.5859408!4d20.7309988
	- 42.60713940,20.59474100 = https://www.google.com.br/maps/place//@42.6071394,20.5947410,9z/data=!4m5!3m4!1s0x0:0x0!8m2!3d42.6071394!4d20.5947410

- if user enters more than one word, we have to weight the results in the following way:
	- consider all words together in the order user entered them;
	- consider all words togehter in any order;
	- consider if all words are in the text (separated and in any order);
	- consider if any of the words are present in the text;

- highlight in the result list all words that user entered;

### License

folkloriyne is an open-source software licensed under the [MIT license](http://opensource.org/licenses/MIT)
