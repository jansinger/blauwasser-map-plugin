import { createWriteStream } from "fs";
import archiver from "archiver";

const pluginName = "blauwasser-map-plugin";
const directories = ["assets", "includes", "lang"];
const files = ["index.php", "blauwasser-map-plugin.php", "LICENSE"];

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

for (let file of files) {
  archive.file(`${file}`, { name: `${pluginName}/${file}` });
}

archive.finalize();
