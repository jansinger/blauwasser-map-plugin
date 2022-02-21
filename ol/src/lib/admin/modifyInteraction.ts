import Geometry from "ol/geom/Geometry";
import { Modify } from "ol/interaction";
import VectorSource from "ol/source/Vector";

export const modifyInteraction = (source: VectorSource<Geometry>) => {
  return new Modify({
    source,
    hitDetection: true,
    deleteCondition: () => false,
    insertVertexCondition: () => false,
  });
};
