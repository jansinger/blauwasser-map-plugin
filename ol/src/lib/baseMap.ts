import { settings } from "./settings";
import { ScaleLine, defaults as defaultControls } from "ol/control";
import { seamap } from "./layers/seamap";
import { Map, View } from "ol";
import apply from "ol-mapbox-style";

export const baseMap = async (
  target: HTMLElement,
  center: number[],
  zoom: number
) => {
  const { key, map } = settings;
  const myMap = new Map({
    target,
    controls: defaultControls().extend([new ScaleLine({ units: "nautical" })]),
    view: new View({
      center,
      zoom,
    }),
  });
  await apply(myMap,`https://api.maptiler.com/maps/${map}-v2/style.json?key=${key}`);
  myMap.addLayer(seamap());

  return myMap;
};
