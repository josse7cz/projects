package uhk.josef.kanvas.rasterization;

import java.util.List;

/**
 * @author Josef Janda 2016 
 * Interfejs pro vypl�ov�n� scanlinem
 */
public interface PolygonRasterizer {
	PolygonRasterizerScanline drawPolygon(List<Point> points);

}
