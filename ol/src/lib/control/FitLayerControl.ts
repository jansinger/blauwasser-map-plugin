//
// Define reset center control.
//

import { Control } from "ol/control";
import type { Options } from "ol/control/Control";
import type Geometry from "ol/geom/Geometry";
import type Layer from "ol/layer/Layer";
import type LayerRenderer from "ol/renderer/Layer";
import type VectorSource from "ol/source/Vector";

type FitLayerControlSource = Layer<VectorSource<Geometry>, LayerRenderer<any>>;

interface FitLayerControlOptions extends Options {
  layer: FitLayerControlSource;
}

export class FitLayerControl extends Control {
  readonly layer: FitLayerControlSource;
  /**
   * @param {Object} [opt_options] Control options.
   */
  constructor(options = {} as FitLayerControlOptions) {
    const button = document.createElement("button");
    button.innerHTML = "&#11034;";
    button.title = "Fit to all markers";

    const element = document.createElement("div");
    element.className = "fit-layer ol-unselectable ol-control";
    element.appendChild(button);

    super({
      element: element,
      target: options.target,
    });

    this.layer = options.layer;

    button.addEventListener("click", this.handleFitLayer.bind(this), false);
  }

  handleFitLayer(e: Event) {
    e.preventDefault();
    if (this.layer) {
      const view = this.getMap().getView();
      view.fit(this.layer.getSource().getExtent(), {
        padding: Array(4).fill(50),
        minResolution: view.getResolutionForZoom(13),
      });
    }
  }
}
