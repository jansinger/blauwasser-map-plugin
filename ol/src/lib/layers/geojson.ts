import VectorLayer from "ol/layer/Vector";
import VectorSource from "ol/source/Vector";
import GeoJSON from "ol/format/GeoJSON";
import { Cluster } from "ol/source";
import { Circle as CircleStyle, Fill, Stroke, Style, Text } from "ol/style";

const clusterStyle = (size: number) =>
  new Style({
    image: new CircleStyle({
      radius: 15,
      stroke: new Stroke({
        color: "#fff",
      }),
      fill: new Fill({
        color: "#003363",
      }),
    }),
    text: new Text({
      text: size.toString(),
      fill: new Fill({
        color: "#fff",
      }),
    }),
  });

const source = (src: string) =>
  new VectorSource({
    url: src,
    format: new GeoJSON(),
  });

export const clusterSource = (src: string) =>
  new Cluster({
    distance: 40,
    minDistance: 20,
    source: source(src),
  });

const styleCache = {};
export const geojson = (src: string, defaultStyle: Style) =>
  new VectorLayer({
    source: clusterSource(src),
    style: function (feature) {
      const size = feature.get("features").length;
      if (!styleCache[size]) {
        styleCache[size] = size === 1 ? defaultStyle : clusterStyle(size);
      }
      return styleCache[size];
    },
    properties: {
      type: "FeatureLayer",
    },
  });
