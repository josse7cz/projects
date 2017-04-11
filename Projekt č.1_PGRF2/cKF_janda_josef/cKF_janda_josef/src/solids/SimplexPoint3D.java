package solids;


import java.util.List;

import transforms.Point3D;

public class SimplexPoint3D extends SolidPoint3D {
	public SimplexPoint3D() {

	}

	public SimplexPoint3D(boolean setLiner) {
		if (setLiner==false) {

			vertices.add(new Point3D(0, 0, 0));
			vertices.add(new Point3D(0, 1, 0));
			vertices.add(new Point3D(1, 0, 0));
			vertices.add(new Point3D(0, 0, 1));
			colors.add(0xFF3399);
			colors.add(0x99FF33);
			colors.add(0xFF9900);
			colors.add(0x33FFFF);
			colors.add(0xFF3399);
			colors.add(0x99FF33);
			colors.add(0xFF9900);
			colors.add(0x33FFFF);
			colors.add(0xFF3399);
			colors.add(0x99FF33);
			colors.add(0xFF9900);
			colors.add(0x33FFFF);

			indices.add(0);
			indices.add(1);
			indices.add(2);

			indices.add(3);
			indices.add(2);
			indices.add(1);
			

			indices.add(2);
			indices.add(0);
			indices.add(3);
		

			indices.add(0);
			indices.add(3);
			indices.add(1);
		} else {
			vertices.add(new Point3D(0, 0, 0));// vrcholy pro liner
			vertices.add(new Point3D(1, 0, 0));
			vertices.add(new Point3D(0, 1, 0));
			vertices.add(new Point3D(0, 0, 1));
			colors.add(0xFF3399);
			colors.add(0x99FF33);
			colors.add(0x33FFFF);

			indices.add(0);// hrany pro liner
			indices.add(1);
			indices.add(1);
			indices.add(2);
			indices.add(2);
			indices.add(0);
			indices.add(0);
			indices.add(3);
			indices.add(1);
			indices.add(3);
			indices.add(2);
			indices.add(3);
		}

		// Integer[] ints = new Integer[]{0, 1, 2};
		// indices = new ArrayList<>(Arrays.asList(ints));
		//
		// vertices= new ArrayList<>();
		// vertices.add(new Point3D(1, 0, 0));
		// vertices.add(new Point3D(0, 1, 0));
		// vertices.add(new Point3D(0, 0, 1));

	}

	@Override
	public List<Integer> colors() {
		// TODO Auto-generated method stub
		return colors;
	}
}
