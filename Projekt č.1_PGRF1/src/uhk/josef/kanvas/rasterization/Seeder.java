
package uhk.josef.kanvas.rasterization;
/**
 * tøída pro vyplòování polygonu pomocí záplavového algoritmu 
 * rekurzivní metoda seedfill, zjistí barvu pixelu vedle sebe a když je to barva pozadí 
 * tak ji zmìní až narazí na hranici danou barvou polygonu
 * vyplòování funguje jen u mnohoúhelníku a u kružnice
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
