import { Style } from "ol/style";
import { singleFeature } from "./lib/layers/singlefeature";

export interface Settings {
  key: string;
  map: string;
  assetsUrl: string;
  restApi: string;
  center: number[];
  zoom: number;
}

export interface MapInstance {
  elementId?: string;
  title?: string;
  image?: string;
  link?: string;
  categories?: string;
  tags?: string;
  src?: string;
  style?: Style;
  layer?: ReturnType<typeof singleFeature>;
  center?: number[];
  zoom?: number;
  show_marker?: boolean;
}
