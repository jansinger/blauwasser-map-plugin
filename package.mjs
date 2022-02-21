import { createWriteStream, readFileSync, writeFileSync } from "fs";
import archiver from "archiver";
import pkg from "./package.json";

const pluginName = "blauwasser-osm-integration";
const directories = ["assets", "includes", "lang"];
const files = ["index.php", "version.php", "LICENSE"];

const output = createWriteStream(`${pluginName}.zip`);
const archive = archiver("zip");

output.on("close", function () {
  console.log(archive.pointer() + " total bytes");
  console.log(
    "archiver has been finalized and the output file descriptor has closed."
  );
});

archive.on("error", function (err) {
  throw err;
});

archive.pipe(output);

for (let dir of directories) {
  archive.directory(`${dir}/`, `${pluginName}/${dir}`);
}

writeFileSync(
  "version.php",
  `<?php\ndefine('BLAUWASSER_OSM_INTEGRATION_VERSION', '${pkg.version}');`
);

for (let file of files) {
  archive.file(`${file}`, { name: `${pluginName}/${file}` });
}

const content = readFileSync(`${pluginName}.php`, "utf8");
const data = content.replace("___VERSION___", pkg.version);
archive.append(data, { name: `${pluginName}/${pluginName}.php` });

archive.finalize();
