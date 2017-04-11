package uhk.josef.kanvas.rasterization;

import java.util.List;

/**
 * @author Josef Janda 2016 
 * Interfejs pro vyplòování scanlinem
 */
public interface PolygonRasterizer {
	PolygonRasterizerScanline drawPolygon(List<Point> points);

}
