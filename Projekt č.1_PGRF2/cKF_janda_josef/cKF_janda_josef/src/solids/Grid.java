package solids;

import java.awt.Color;
import java.util.ArrayList;
import java.util.List;

import transforms.Cubic;
import transforms.Mat4;
import transforms.Point3D;
import transforms.Bicubic;

public class Grid extends SolidPoint3D {

	private Mat4 cubic;
	private Bicubic grid;
	public static final int points =31;

	public static final Point3D p11 = new Point3D(0, 0, -5);
	public static final Point3D p12 = new Point3D(0, 1, 5);
	public static final Point3D p13 = new Point3D(1, 2, -5);
	public static final Point3D p14 = new Point3D(0, 3, 5);

	public static final Point3D p21 = new Point3D(1, 0, -4);
	public static final Point3D p22 = new Point3D(1, 1, 4);
	public static final Point3D p23 = new Point3D(2, 2, -4);
	public static final Point3D p24 = new Point3D(1, 3, 4);

	public static final Point3D p31 = new Point3D(2, 0, -3);
	public static final Point3D p32 = new Point3D(2, 1, 3);
	public static final Point3D p33 = new Point3D(3, 2, -3);
	public static final Point3D p34 = new Point3D(2, 3, 3);

	public static final Point3D p41 = new Point3D(3, 0, -2);
	public static final Point3D p42 = new Point3D(3, 1, 2);
	public static final Point3D p43 = new Point3D(4, 2, 2);
	public static final Point3D p44 = new Point3D(3, 3, 2);

	public Grid() {
		cubic = Cubic.BEZIER;
		grid = new Bicubic(cubic, p11, p12, p13, p14, p21, p22, p23, p24, p31, p32, p33, p34, p41, p42, p43, p44);
		indices = new ArrayList<>();
		vertices = new ArrayList<>();
		colors = new ArrayList<>();
		
		for (int i = 0; i <= points/2; i++) {
		 int r = (int) (Math.random() * 255 );
		 int g = (int) (Math.random() * 255 );
		 int b = (int) (Math.random() * 255 );
		 colors.add(Math.abs(new Color(r, g, b).getRGB()));
		 colors.add(Math.abs(new Color(r, g, b).getRGB()));
		 colors.add(Math.abs(new Color(r, g, b).getRGB()));
		 colors.add(Math.abs(new Color(r, g, b).getRGB()));
	
		 }
		
//		colors.add(0x33FFFF);
//		colors.add(0xFF9933);
//		colors.add(0xCC99FF);
		// points
		for (int i = 0; i <= points; i++) {
			for (int j = 0; j <= points; j++) {
				vertices.add(grid.compute((double) i / points, (double) j / points));
			}
		}

		// generate indices dva cykly (propojení bodu)
		for (int i = 0; i < points; i++) {
			for (int j = 0; j < points; j++) {
				
				indices.add(i * (points + 1) + j);
				
				indices.add(i * (points + 1) + j + 1);
				
				
				indices.add((i + 1) * (points + 1) + j);
				
				indices.add(i * (points + 1) + j + 1);
				
				indices.add((i + 1) * (points + 1) + j);
				
				indices.add((i + 1) * (points + 1) + j + 1);
				
			}
		}

		// Random color
//		 for (int i = 0; i < indices.size(); i++) {
//		 int r = (int) (Math.random() * 255 );
//		 int g = (int) (Math.random() * 255 );
//		 int b = (int) (Math.random() * 255 );
//		 color.add(new Color(r, g, b).getRGB());
//		 }
	}

@Override
	public List<Integer> colors() {
		// TODO Auto-generated method stub
		return colors;
	}

}
