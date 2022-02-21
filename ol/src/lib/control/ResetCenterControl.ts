//
// Define reset center control.
//

import { Control } from "ol/control";
import type { Options } from "ol/control/Control";
import Point from "ol/geom/Point";
import SimpleGeometry from "ol/geom/SimpleGeometry";

interface ResetCenterControlOptions extends Options {
  geometry: SimpleGeometry;
}

export class ResetCenterControl extends Control {
  readonly geometry: SimpleGeometry;
  /**
   * @param {Object} [opt_options] Control options.
   */
  constructor(options = {} as ResetCenterControlOptions) {
    const button = document.createElement("button");
    button.innerHTML = "&odot;";
    button.title = "Center current marker position";

    const element = document.createElement("div");
    element.className = "reset-center ol-unselectable ol-control";
    element.appendChild(button);

    super({
      element: element,
      target: options.target,
    });

    this.geometry = options.geometry ?? new Point([0, 0]);

    button.addEventListener("click", this.handleResetCenter.bind(this), false);
  }

  handleResetCenter(e: Event) {
    e.preventDefault();
    this.getMap().getView().setCenter(this.geometry.getCoordinates());
  }
}
