import { Feature } from "ol";
import Point from "ol/geom/Point";
import VectorLayer from "ol/layer/Vector";
import VectorSource from "ol/source/Vector";
import { MapInstance } from "../../types";

export const singleFeature = ({
  center,
  title = "",
  image = "",
  link = "",
  style,
}: MapInstance) => {
  const iconFeature = new Feature(new Point(center));
  iconFeature.setProperties({ title, image, link });
  return new VectorLayer({
    style,
    source: new VectorSource({ features: [iconFeature] }),
    properties: { type: "FeatureLayer" },
  });
};
