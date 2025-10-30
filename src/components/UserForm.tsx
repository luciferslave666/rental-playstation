// src/components/UserForm.tsx
"use client";

import { useState, useEffect } from 'react';
import { UserAdminData } from '@/src/types'; // Import tipe User (tanpa password)

interface UserFormProps {
  initialData: UserAdminData | null;
  onSuccess: () => void;
  apiError: string | null; 
  setApiError: (error: string | null) => void;
}

export default function UserForm({ 
  initialData, 
  onSuccess, 
  apiError, 
  setApiError 
}: UserFormProps) {
  const [nama, setNama] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState(''); // Selalu kosong di awal
  const [role, setRole] = useState('kasir'); // Default role
  const [isLoading, setIsLoading] = useState(false);

  const isEditMode = initialData !== null;

  useEffect(() => {
    if (initialData) {
      setNama(initialData.nama);
      setUsername(initialData.username);
      setRole(initialData.role);
      setPassword(''); // Password SELALU dikosongkan saat edit
    } else {
      // Reset form
      setNama('');
      setUsername('');
      setPassword('');
      setRole('kasir');
    }
    setApiError(null); 
  }, [initialData, setApiError]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setApiError(null);

    const url = isEditMode 
      ? `/api/admin/user/${initialData.id}` 
      : '/api/admin/user';
    const method = isEditMode ? 'PUT' : 'POST';
    
    // Siapkan data untuk dikirim
    const dataToSend: any = {
      nama,
      username,
      role,
    };
    
    // Hanya kirim password jika diisi
    if (password.trim() !== '') {
      dataToSend.password = password;
    }

    try {
      const res = await fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dataToSend),
      });

      const data = await res.json();
      
      if (!res.ok) {
        throw new Error(data.error || 'Gagal menyimpan data');
      }
      
      onSuccess();
      
    } catch (err) {
      if (err instanceof Error) {
        setApiError(err.message);
      } else {
        setApiError('Terjadi kesalahan tidak diketahui');
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4 text-gray-700">
      {apiError && (
        <div className="p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
          {apiError}
        </div>
      )}
      
      <div>
        <label className="block text-sm font-medium mb-1">Nama Lengkap*</label>
        <input
          type="text"
          value={nama}
          onChange={(e) => setNama(e.target.value)}
          required
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Username*</label>
        <input
          type="text"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          required
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Password*</label>
        <input
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          // Password hanya 'required' saat BUKAN edit mode
          required={!isEditMode} 
          placeholder={isEditMode ? "(Kosongkan jika tidak ingin diubah)" : ""}
          className="w-full p-2 border border-gray-300 rounded-md"
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Role*</label>
        <select
          value={role}
          onChange={(e) => setRole(e.target.value)}
          required
          className="w-full p-2 border border-gray-300 rounded-md bg-white"
        >
          <option value="kasir">Kasir</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <button
        type="submit"
        disabled={isLoading}
        className="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
      >
        {isLoading ? 'Menyimpan...' : (initialData ? 'Update User' : 'Simpan User Baru')}
      </button>
    </form>
  );
}