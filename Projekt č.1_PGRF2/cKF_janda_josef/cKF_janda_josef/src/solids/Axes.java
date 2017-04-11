package solids;

import java.util.List;

import transforms.Point3D;
/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */
public class Axes extends SolidPoint3D {

	public Axes() {

		vertices.add(new Point3D(0, 0, 0));// vrcholy pro liner
		vertices.add(new Point3D(2, 0, 0));
		vertices.add(new Point3D(0, 2, 0));
		vertices.add(new Point3D(0, 0, 2));

		indices.add(0);// hrany pro liner
		indices.add(1);

		indices.add(2);
		indices.add(0);

		colors.add(0x00DDFF);
	//	colors.add(0xFF9900);
		colors.add(0x99FF33);
		colors.add(0xFF0000);
		indices.add(0);
		indices.add(3);

	}

	public List<Integer> colors() {

		return colors;
	}
}
