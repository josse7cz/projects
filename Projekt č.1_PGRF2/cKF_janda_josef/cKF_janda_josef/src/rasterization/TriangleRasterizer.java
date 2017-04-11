package rasterization;
/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */
public interface TriangleRasterizer {
	TriangleRasterizer draw(double x1, double y1, double z1, double x2, double y2, double z2, double x3, double y3,
			double z3, int color);

}
