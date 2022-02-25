import XYZ from "ol/source/XYZ";
import TileLayer from "ol/layer/WebGLTile";

export const seamap = () =>
  new TileLayer({
    source: new XYZ({
      url: "https://t1.openseamap.org/seamark/{z}/{x}/{y}.png",
    }),
  });
