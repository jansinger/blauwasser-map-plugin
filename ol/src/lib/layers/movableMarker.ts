import { settings } from "$lib/settings";
import { defaultMarkerStyle } from "$lib/styles/defaultMarkerStyle";
import Point from "ol/geom/Point";
import { singleFeature } from "./singlefeature";

export const movableMarker = (center: number[]) => {
  const style = defaultMarkerStyle(settings.assetsUrl);
  const layer = singleFeature({
    title: "Geotag location",
    style,
    center,
  });
  const point = layer.getSource().getFeatures()[0].getGeometry() as Point;
  return {
    layer,
    point,
  };
};
