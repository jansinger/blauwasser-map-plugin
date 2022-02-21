import { Icon, Style } from "ol/style";

export const defaultMarkerStyle = (assetUrl: string) =>
  new Style({
    image: new Icon({
      crossOrigin: "anonymous",
      // For Internet Explorer 11
      imgSize: [100, 100],
      src: `${assetUrl}pics/marker-bw.png`,
      scale: 0.4,
      opacity: 1,
      anchor: [0.5, 100],
      anchorXUnits: "fraction",
      anchorYUnits: "pixels",
    }),
  });
