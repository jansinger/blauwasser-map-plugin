import VectorTileLayer from "ol/layer/VectorTile";
import { applyStyle } from 'ol-mapbox-style';

const attributions =
  '<a href="https://www.maptiler.com/copyright/" target="_blank" rel="noopener">&copy; MapTiler</a> ' +
  '<a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">&copy; OpenStreetMap contributors</a>';

export const osm = (apiKey: string, mapStyle: string) =>
 {
  let layer = new VectorTileLayer({
    declutter: true
  });
  applyStyle(layer,`https://api.maptiler.com/maps/${mapStyle}-v2/style.json?key=${apiKey}`);
  return layer;
 }
