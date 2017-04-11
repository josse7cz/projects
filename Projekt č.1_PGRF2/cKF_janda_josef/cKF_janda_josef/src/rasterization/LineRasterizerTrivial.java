package rasterization;

import java.awt.image.BufferedImage;
/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */
public class LineRasterizerTrivial implements LineRasterizer {
	private final BufferedImage img;

	public LineRasterizerTrivial(final BufferedImage img) {
		this.img = img;

	}

	@Override
	public LineRasterizer drawLine(int x1, int y1, int x2, int y2, int color) {// vykreslování

		if (color == 0) {
			color = 0x00DDFF;

		}
		if (x1 >= 0 && y1 >= 0 && y1 < img.getHeight() && x1 < img.getWidth() && x2 >= 0 && y2 >= 0
				&& y2 < img.getHeight() && x2 < img.getWidth()) {

			if (Math.abs(y2 - y1) <= Math.abs(x2 - x1)) {
				int x;
				if (x2 < x1) {
					x = x1;
					x1 = x2;
					x2 = x;
					x = y1;
					y1 = y2;
					y2 = x;
				}
				int dy = y2 - y1;
				int dx = x2 - x1;
				x = x1;
				int y = y1;
				int p = 2 * (Math.abs(dy) - dx);
				int k1 = 2 * Math.abs(dy);
				int k2 = 2 * (Math.abs(dy) - dx);

				while (x < x2) {
					++x;
					if (p < 0) {
						p += k1;
					} else {
						p += k2;
						y = dy > 0 ? ++y : --y;
					}

					if (x1 >= 0 && y1 >= 0 && y1 < img.getHeight() && x1 < img.getWidth() && x2 >= 0 && y2 >= 0
							&& y2 < img.getHeight() && x2 < img.getWidth()) {

						img.setRGB(x, y - 1 / 2, color);
						img.setRGB(x, y, color);
						img.setRGB(x + 1 / 2, y, color);
					}
				}
			} else {
				int pom;

				if (y2 < y1) {
					pom = x1;
					x1 = x2;
					x2 = pom;
					pom = y1;
					y1 = y2;
					y2 = pom;
				}
				int dy = y2 - y1;
				int dx = x2 - x1;
				pom = x1;
				int y = y1;
				int p = 2 * (Math.abs(dx) - dy);
				int k1 = 2 * Math.abs(dx);
				int k2 = 2 * (Math.abs(dx) - dy);

				while (y < y2) {
					++y;

					if (p < 0) {
						p += k1;
					} else {
						p += k2;
						pom = dx > 0 ? ++pom : --pom;
					}

					img.setRGB(pom - 1 / 2, y, color);
					img.setRGB(pom, y, color);
					img.setRGB(pom + 1 / 2, y, color);
				}
			}
		}
		return this;

	}

}