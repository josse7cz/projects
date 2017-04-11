package uhk.josef.kanvas.rasterization;

import java.awt.Color;
import java.awt.Graphics;
import java.awt.image.BufferedImage;

/**
 * Matematika pro kresbu úseèky + =use4ka s antialiasingem ten má však chyby
 * 
 * @author Josef Janda 2016
 *
 */
public class LineRasterizerTrivial implements LineRasterizer {
	private final BufferedImage img;

	public LineRasterizerTrivial(final BufferedImage img) {

		this.img = img;
	}

	public LineRasterizer drawLine(int x1, int y1, int x2, int y2, final int color) {// vykreslování
		int err;
		int dx;
		int dy;
		dx = x2 - x1;
		dy = y2 - y1;
		err = 0;

		if (x2 < x1) {
			int pom = x1;
			x1 = x2;
			x2 = pom;
			pom = y1;
			y1 = y2;
			y2 = pom;
			dx = -dx;
			dy = -dy;

		}
		int sign;
		sign = (int) Math.signum(dy);
		if (dy < 1) {
			dy = -dy;
		}

		do {

			 if (x1 >= 0 && y1 >= 0 && y1 < img.getHeight() && x1 <
			 img.getWidth()) {
			 try {
			img.setRGB(x1, y1, color);
			
			
			
			 } catch (Exception e) {
				 System.out.println(e);
						 }
			
			 } else{
				img.setRGB(1, 1, color);
				break;
			 }
			if (err > 0) {
				x1 = x1 + 1;
				err = err - dy;
			} else {

				y1 = y1 + sign; // reseno znamenko kvuli ridicim osam
				err = err + dx;
			}
		} while ((x1 <= x2));

		return this;
	}

	public LineRasterizer drawLineAA(int x1, int y1, int x2, int y2, final int color) {// vykreslování
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

				double w = (double) (-p) / (double) dx;
				this.drawPixelTransparent(x, y - 1, color, w);
				this.drawPixelTransparent(x, y, color, 1.0 - w);
				this.drawPixelTransparent(x + 1, y, color, -w);
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
				double w = (double) (-p) / (double) dx;
				this.drawPixelTransparent(pom - 1, y, color, w);
				this.drawPixelTransparent(pom, y, color, 1.0 - w);
				this.drawPixelTransparent(pom + 1, y, color, -w);
			}
		}
		return this;
	}

	private final int zoom = 1;

	private void drawPixelTransparent(int x, int y, int barva, double k) {
		Graphics g = img.getGraphics();
		if (k < 0.0) {
			k = 0.0;
		}
		if (k > 1.0) {
			k = 1.0;
		}
		int r = (int) ((double) new Color(barva).getRed() * k + (1.0 - k) * 255.0);
		int gr = (int) ((double) new Color(barva).getGreen() * k + (1.0 - k) * 255.0);
		int b = (int) ((double) new Color(barva).getBlue() * k + (1.0 - k) * 255.0);
		if (r > 255) {
			r = 255;
		}
		if (gr > 255) {
			gr = 255;
		}
		if (b > 255) {
			b = 255;
		}
		g.setColor(new Color(r, gr, b));
		if (k > 0.1) {
			g.fillRect(x * this.zoom - this.zoom / 2, this.img.getHeight() - y * this.zoom - this.zoom / 2, this.zoom,
					this.zoom);

		}
	}
}
