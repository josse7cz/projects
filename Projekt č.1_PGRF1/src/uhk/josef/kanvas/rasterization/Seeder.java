
package uhk.josef.kanvas.rasterization;
/**
 * t��da pro vypl�ov�n� polygonu pomoc� z�plavov�ho algoritmu 
 * rekurzivn� metoda seedfill, zjist� barvu pixelu vedle sebe a kdy� je to barva pozad� 
 * tak ji zm�n� a� naraz� na hranici danou barvou polygonu
 * vypl�ov�n� funguje jen u mnoho�heln�ku a u kru�nice
 * 
 * @author PGRF FIM UHK and JOSEF JANDA
 * @version 2016
 */
import java.awt.Color;
import java.awt.image.BufferedImage;

public class Seeder {
	private BufferedImage img;
	int background;

	public Seeder( BufferedImage img) {
		this.img = img;
	}

	public Seeder( BufferedImage img, int x) {
		this.img = img;
	}

	public void seedFill(int x, int y, int newColor, int oldColor) {
		Color c =new Color(img.getRGB(x, y),true);
		Color cOld =new Color(oldColor);
		Color cNew =new Color(newColor);
		
		if (x >= 0 && x < img.getWidth() && y >= 0 && y < img.getHeight() && c.equals(cOld)//zjisti hranici a barvu
				&&(!c.equals(cNew))) {
			img.setRGB(x, y, newColor);
			seedFill(x + 1, y, newColor, oldColor);
			seedFill(x - 1, y, newColor, oldColor);
			seedFill(x, y + 1, newColor, oldColor);
			seedFill(x, y - 1, newColor, oldColor);
					
		}
	}
}
