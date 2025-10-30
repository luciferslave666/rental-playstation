import NextAuth, { DefaultSession } from "next-auth";
import { JWT } from "next-auth/jwt";

// Tipe kustom untuk data di JWT
declare module "next-auth/jwt" {
  interface JWT {
    id: string;
    role: string;
    username: string;
  }
}

// Tipe kustom untuk data di 'session.user'
declare module "next-auth" {
  interface Session {
    user: {
      id: string;
      role: string;
      username: string;
    } & DefaultSession["user"]; // Gabung dengan tipe default
  }
  
  // Tipe kustom untuk 'user' yang dikembalikan 'authorize'
  interface User {
    id: string;
    role: string;
    username: string;
  }
}