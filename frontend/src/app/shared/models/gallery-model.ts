
export interface Gallery {
  id: string;
  name: string;
  description: string;
  coverImage: string | null;
  images?: Image[];
}

export interface Image {
  id: string;
  name: string;
  url: string;
  alt?: string;
}
