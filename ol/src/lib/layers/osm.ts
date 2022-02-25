import TileLayer from "ol/layer/WebGLTile";
import TileJSON from "ol/source/TileJSON";

const attributions =
  '<a href="https://www.maptiler.com/copyright/" target="_blank" rel="noopener">&copy; MapTiler</a> ' +
  '<a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">&copy; OpenStreetMap contributors</a>';

export const osm = (apiKey: string, mapStyle: string) =>
  new TileLayer({
    source: new TileJSON({
      url: `https://api.maptiler.com/maps/${mapStyle}/tiles.json?key=${apiKey}`,
      tileSize: 512,
      crossOrigin: "anonymous",
    }),
  });
