package rasterization;

import java.awt.Color;
import java.awt.Graphics;
import java.awt.image.BufferedImage;
/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */
public class TriangleRasterizerScan implements TriangleRasterizer {
	private final BufferedImage img;
	private final double[][] depthImg;

	public TriangleRasterizerScan(final BufferedImage img) {
		this.img = img;
		depthImg = new double[img.getHeight()][img.getWidth()];

	}

	public void clear(int color, double z) {
		// TODO vyplnit img hodnotou color a depthImg
		Graphics gr = img.getGraphics();
		gr.setColor(new Color(color));
		gr.fillRect(0, 0, img.getWidth(), img.getHeight());

		for (int i = 0; i < depthImg.length; i++) {
			for (int j = 0; j < depthImg[0].length; j++) {
				depthImg[i][j] = z;
			}
		}
	}

	@Override
	public TriangleRasterizer draw(

			double x1, double y1, double z1, double x2, double y2, double z2, double x3, double y3, double z3,
			int color) {

		// TODO zaridit, ze y1 <= y2 <= y3...
		double pom;
		if (y1 >= y2) {// y1<y2
			pom = y1;
			y1 = y2;
			y2 = pom;
			double pom1;
			pom1 = x1;
			x1 = x2;
			x2 = pom1;

			double pom2 = z1;
			z1 = z2;
			z2 = pom2;

		}
		if (y2 >= y3) {
			pom = y2;
			y2 = y3;
			y3 = pom;

			double pom3 = x2;
			x2 = x3;
			x3 = pom3;
			double pom4 = z2;
			z2 = z3;
			z3 = pom4;
		}
		if (y1 >= y2) {
			pom = y1;
			y1 = y2;
			y2 = pom;
			double pom5 = x1;
			x1 = x2;
			x2 = pom5;
			double pom6 = z1;
			z1 = z2;
			z2 = pom6;
		}

		// interpolace
		for (int y = Math.max((int) y1 + 1, 0); y <= Math.min(y2, img.getHeight() - 1); y++) {
			final double ta = (y - y1) / (y2 - y1);
			double xa = x1 * (1 - ta) + x2 * ta;
			double za = z1 * (1 - ta) + z2 * ta;
			// TODO opravit na y1<->y3

			final double tb = (y - y1) / (y3 - y1);
			double xb = x1 * (1 - tb) + x3 * tb;
			double zb = z1 * (1 - tb) + z3 * tb;

			// TODO zaridit xa <= xb

			if (xa >= xb) {
				double pomXA = xa;
				xa = xb;
				xb = pomXA;
				double pomZA = za;
				za = zb;
				zb = pomZA;
			}
			for (int x = Math.max((int) xa + 1, 0); x <= Math.min(xb, img.getWidth() - 1); x++) { // //omezeni
																									// na
																									// frame

				final double t = (x - xa) / (xb - xa);
				final double z = za * (1 - t) + zb * t; // TODO

				if (z < depthImg[y][x]) {

					img.setRGB(x, y, color);

					depthImg[y][x] = z;

				}

			}
		}

		for (int y = Math.max((int) y2 + 1, 0); y <= Math.min(y3, img.getHeight() - 1); y++) {// omezeni
																								// na
																								// frame
			final double ta = (y - y2) / (y3 - y2);
			double xa = x2 * (1 - ta) + x3 * ta;
			double za = z2 * (1 - ta) + z3 * ta;
			// TODO opravit na y2<->y3

			final double tb = (y - y1) / (y3 - y1);
			double xb = x1 * (1 - tb) + x3 * tb;
			double zb = z1 * (1 - tb) + z3 * tb;

			// TODO zaridit xa <= xb

			if (xa >= xb) {
				double pomXA = xa;
				xa = xb;
				xb = pomXA;
				double pomZA = za;
				za = zb;
				zb = pomZA;
			}
			for (int x = Math.max((int) xa + 1, 0); x <= Math.min(xb, img.getWidth() - 1); x++) {// omezeni
																									// na
																									// frame
				final double t = (x - xa) / (xb - xa);
				final double z = za * (1 - t) + zb * t; // TODO

				if (z < depthImg[y][x]) {

					img.setRGB(x, y, color);

					depthImg[y][x] = z;

				}
			}
		}
		return this;
	}

}
