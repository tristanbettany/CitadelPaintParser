# Citadel Paint Parser

This repo takes as input the JSON response from GW's search results on their website as a static `.json` file.

This file is then processed into JSON data that is more digestable. It also parses the product SVG files to extract the color of the paint as a Hex Code.

The result of the script is stored in this repo in a readable formatted version (in the `dist` folder).

Hopefully this simplified version of this data is useful to someone. It could help creating an inventory system for your paints.

## Usage

If you want to run this yourself and generate a newly parsed file, then do the following...

First replace the `searchResponse.json` file (in the `data` folder) with JSON from the GW website search response you want to parse.

Then run the below commands:

```
./app.bat build
./app.bat exec
```

The resulting file will be placed in the storage folder called `products.json`

If you want to make this more readable then format it with an online formatter or your IDE.

Move the formatted file to the dist folder.

