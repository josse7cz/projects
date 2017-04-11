package uhk.josef.kanvas.rasterization;

import java.awt.image.BufferedImage;

/**
 * tøída pro kreslení kruhu s metodou pro tvorbu vysece pomocí úhlù.kliknutím
 * tlaèítka myši a držením je tvoøena úseèka, která se po puštìní tlaèítka
 * zafixuje, dalím kliknutím a tažením se kreslí výseè pro potøeby výseèe je
 * tøeba vytvorit pøímku zhruba vodorovnì
 * 
 * @author PGRF FIM UHK and JOSEF JANDA
 * @version 2016
 */

public class CircleRasterizationTrivial implements CircleRasterizer {
	private final BufferedImage img;

	public CircleRasterizationTrivial(final BufferedImage img) {
		this.img = img;
	}

	public CircleRasterizationTrivial drawCircle(int x1, int y1, int x2, int y2, final int color) {// metoda
																									// pro
																									// vykreslení
																									// kruhu

		img.setRGB(x1, y1, color);
		int radius = (int) (Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)));
		int d1 = (2 * radius);
		int yi = radius;

		for (int xi = 0; xi <= yi; xi++) {
			if (d1 < 0) {
				d1 = d1 + (4 * xi) + 6;
			} else {
				d1 = d1 + 4 * (xi - yi) + 10;
				yi = yi - 1;
			}
			;
			if (x1 + xi >= 0 && y1 - yi >= 0 && y1 - yi <= img.getHeight() && x1 + xi < img.getWidth()) {

				// podminka osetreni mimo frame
				// vykresleni bodu v kazdem oktanu
				img.setRGB((int) (x1 + xi), (int) (y1 - yi), 0xffffff);
			} // 1
			if (x1 + yi >= 0 && y1 - xi >= 0 && y1 - xi < img.getHeight() && x1 + yi < img.getWidth()) {

				img.setRGB((int) (x1 + yi), (int) (y1 - xi), 0xffffff);
			} // 2
			if (x1 + yi >= 0 && y1 + xi >= 0 && y1 + xi < img.getHeight() && x1 + yi < img.getWidth()) {
				img.setRGB((int) (x1 + yi), (int) (y1 + xi), 0xffffff);
			} // 3
			if (x1 + xi >= 0 && y1 + yi >= 0 && y1 + yi < img.getHeight() && x1 + xi < img.getWidth()) {
				img.setRGB((int) (x1 + xi), (int) (y1 + yi), 0xffffff);
			} // 4

			if (x1 - xi >= 0 && y1 + yi >= 0 && y1 + yi < img.getHeight() && x1 - xi < img.getWidth()) {
				img.setRGB((int) (x1 - xi), (int) (y1 + yi), 0xffffff);
			} // 5
			if (x1 - yi >= 0 && y1 + xi >= 0 && y1 + xi < img.getHeight() && x1 - yi < img.getWidth()) {
				img.setRGB((int) (x1 - yi), (int) (y1 + xi), 0xffffff);
			} // 6
			if (x1 - yi >= 0 && y1 - xi >= 0 && y1 - xi < img.getHeight() && x1 - yi < img.getWidth()) {
				img.setRGB((int) (x1 - yi), (int) (y1 - xi), 0xffffff);
			} // 7
			if (x1 - xi >= 0 && y1 - yi >= 0 && y1 - yi < img.getHeight() && x1 - xi < img.getWidth()) {
				img.setRGB((int) (x1 - xi), (int) (y1 - yi), 0xffffff);
			} // 8

		}

		return this;
	}

	public CircleRasterizer drawCirclePart(int x1, int y1, int x2, int y2, int x3, int y3, int color) {// metoda
																										// pro
																										// vysec

		img.setRGB(x1, y1, color);
		int radius = (int) (Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)));

		double alpha = Math.atan2((y2 - y1), (x2 - x1));
		double beta = Math.atan2((y3 - y1), (x3 - x1));
		double yi = 0;
		double xi = 0;

		for (double fi = alpha; fi < beta * 2.5; fi += 0.001) {
			double xr = Math.sin(fi) * radius;
			double yr = Math.cos(fi) * radius;
			yi = (yr);
			xi = (xr);
			if (x1 + yi >= 0 && y1 + xi >= 0 && y1 + xi < img.getHeight() && x1 + yi < img.getWidth()) {
				img.setRGB((int) (x1 + yi), (int) (y1 + xi), 0xffffff);

			}
		}
		if (xi != 0 && yi != 0) {
			LineRasterizerTrivial lrt = new LineRasterizerTrivial(img);
			lrt.drawLine(x1, y1, (int) (x1 + yi), (int) (y1 + xi), 0xffffff);
		}

		return this;

	}
}

// img.setRGB(300, 300, color); //metoda rotace stredu pomoci matice(tøeba
// naimportovat knihovnu transforms)
// double radius = (Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2)));
// for (double fi = 0; fi < (Math.PI) * 2; fi += 0.01) {
// x1 = (int) (radius * Math.cos(fi));
// y1 = (int) (radius * Math.sin(fi));
//
// Point2D pointSt = new Point2D(x1, y1);
// Mat3 mat = new Mat3Rot2D(fi);
// System.out.println("mat=");
// System.out.println(pointSt.mul(mat));
//
// x2 = (int) pointSt.getX();
// x2 = x2 + 300;
// y2 = (int) pointSt.getY();
// y2 = y2 + 300;
//
// img.setRGB(x2, y2, 0xffffff);
// }
//
// return this;
// }
//
// }