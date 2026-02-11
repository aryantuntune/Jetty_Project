// App Slice following the migration guide
import { createSlice, PayloadAction } from '@reduxjs/toolkit';

type ThemeType = 'light' | 'dark';
type LanguageType = 'en' | 'mr';

interface AppState {
    isOnline: boolean;
    theme: ThemeType;
    language: LanguageType;
}

const initialState: AppState = {
    isOnline: true,
    theme: 'light',
    language: 'en',
};

const appSlice = createSlice({
    name: 'app',
    initialState,
    reducers: {
        setOnlineStatus: (state, action: PayloadAction<boolean>) => {
            state.isOnline = action.payload;
        },
        setTheme: (state, action: PayloadAction<ThemeType>) => {
            state.theme = action.payload;
        },
        setLanguage: (state, action: PayloadAction<LanguageType>) => {
            state.language = action.payload;
        },
    },
});

export const { setOnlineStatus, setTheme, setLanguage } = appSlice.actions;
export default appSlice.reducer;
